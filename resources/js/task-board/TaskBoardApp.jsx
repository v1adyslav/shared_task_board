import { useEffect, useMemo, useState } from 'react';
import axios from 'axios';

export default function TaskBoardApp({ config }) {
    const [board, setBoard] = useState(config.board);
    const [status, setStatus] = useState('Live updates connected.');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [selectedColumnId, setSelectedColumnId] = useState(
        config.board.columns?.[0]?.id ? String(config.board.columns[0].id) : ''
    );

    const columns = useMemo(
        () => [...(board.columns ?? [])].sort((a, b) => a.position - b.position),
        [board.columns]
    );

    const replaceCard = (card) => {
        setBoard((previous) => {
            const columnsWithoutCard = previous.columns.map((column) => ({
                ...column,
                cards: column.cards.filter((existingCard) => existingCard.id !== card.id),
            }));

            const updatedColumns = columnsWithoutCard.map((column) => {
                if (column.id !== card.board_column_id) {
                    return column;
                }

                const cards = [...column.cards, card].sort((a, b) => a.position - b.position);

                return { ...column, cards };
            });

            return { ...previous, columns: updatedColumns };
        });
    };

    const removeCard = (cardId) => {
        setBoard((previous) => ({
            ...previous,
            columns: previous.columns.map((column) => ({
                ...column,
                cards: column.cards.filter((card) => card.id !== cardId),
            })),
        }));
    };

    const moveCard = async (cardId, boardColumnId, position) => {
        const url = config.routes.moveTemplate.replace('__CARD__', String(cardId));
        const response = await axios.patch(url, {
            board_column_id: boardColumnId,
            position,
        });

        replaceCard(response.data.card);
    };

    const updateCard = async (cardId, payload) => {
        const url = config.routes.updateTemplate.replace('__CARD__', String(cardId));
        const response = await axios.put(url, payload);
        replaceCard(response.data.card);
    };

    const deleteCard = async (cardId) => {
        const url = config.routes.deleteTemplate.replace('__CARD__', String(cardId));
        await axios.delete(url);
        removeCard(cardId);
    };

    const createCard = async () => {
        if (!title.trim()) {
            setStatus('Title is required.');
            return;
        }

        try {
            const response = await axios.post(config.routes.store, {
                board_column_id: Number(selectedColumnId),
                title: title.trim(),
                description: description.trim(),
            });

            replaceCard(response.data.card);
            setTitle('');
            setDescription('');
            setStatus('Card created.');
        } catch (error) {
            setStatus(error?.response?.data?.message ?? 'Failed to create card.');
        }
    };

    useEffect(() => {
        if (!selectedColumnId && columns[0]?.id) {
            setSelectedColumnId(String(columns[0].id));
        }
    }, [columns, selectedColumnId]);

    useEffect(() => {
        if (!window.Echo) {
            setStatus('Live updates unavailable.');
            return undefined;
        }

        const channelName = `board.${board.id}`;
        const channel = window.Echo.private(channelName)
            .listen('.card.created', (event) => replaceCard(event.card))
            .listen('.card.updated', (event) => replaceCard(event.card))
            .listen('.card.moved', (event) => replaceCard(event.card))
            .listen('.card.deleted', (event) => removeCard(event.card.id));

        return () => {
            channel.stopListening('.card.created');
            channel.stopListening('.card.updated');
            channel.stopListening('.card.moved');
            channel.stopListening('.card.deleted');
            window.Echo.leave(channelName);
        };
    }, [board.id]);

    return (
        <div className="space-y-4 p-6 text-gray-900">
            <div className="flex items-end gap-3">
                <div className="flex-1">
                    <label htmlFor="new-card-title" className="block text-sm font-medium">
                        New card title
                    </label>
                    <input
                        id="new-card-title"
                        type="text"
                        className="mt-1 w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Task title"
                        value={title}
                        onChange={(event) => setTitle(event.target.value)}
                    />
                </div>
                <div className="flex-1">
                    <label htmlFor="new-card-description" className="block text-sm font-medium">
                        Description
                    </label>
                    <input
                        id="new-card-description"
                        type="text"
                        className="mt-1 w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Optional details"
                        value={description}
                        onChange={(event) => setDescription(event.target.value)}
                    />
                </div>
                <div>
                    <label htmlFor="new-card-column" className="block text-sm font-medium">
                        Column
                    </label>
                    <select
                        id="new-card-column"
                        className="mt-1 border-gray-300 rounded-md shadow-sm"
                        value={selectedColumnId}
                        onChange={(event) => setSelectedColumnId(event.target.value)}
                    >
                        {columns.map((column) => (
                            <option key={column.id} value={column.id}>
                                {column.name}
                            </option>
                        ))}
                    </select>
                </div>
                <button
                    type="button"
                    onClick={createCard}
                    className="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
                >
                    Add card
                </button>
            </div>

            <div className="text-sm text-gray-500">{status}</div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {columns.map((column) => (
                    <section key={column.id} className="border rounded-lg p-4 bg-white">
                        <h3 className="font-semibold mb-3">{column.name}</h3>
                        <div className="space-y-3">
                            {[...column.cards]
                                .sort((a, b) => a.position - b.position)
                                .map((card, index) => {
                                    const prevColumn = columns.find(
                                        (candidate) => candidate.position === column.position - 1
                                    );
                                    const nextColumn = columns.find(
                                        (candidate) => candidate.position === column.position + 1
                                    );

                                    return (
                                        <div
                                            key={card.id}
                                            className="border rounded-md p-3 bg-gray-50 space-y-2"
                                        >
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <p className="font-semibold">{card.title}</p>
                                                    <p className="text-xs text-gray-500">
                                                        by {card.created_by_name ?? 'User'}
                                                    </p>
                                                </div>
                                                <button
                                                    type="button"
                                                    className="text-red-600 text-xs"
                                                    onClick={async () => {
                                                        await deleteCard(card.id);
                                                        setStatus('Card deleted.');
                                                    }}
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                            <p className="text-sm text-gray-700">
                                                {card.description ?? ''}
                                            </p>
                                            <div className="flex gap-2 flex-wrap">
                                                <button
                                                    type="button"
                                                    className="text-xs text-indigo-600"
                                                    onClick={async () => {
                                                        const nextTitle = window.prompt('New title');
                                                        if (!nextTitle) return;
                                                        const nextDescription =
                                                            window.prompt(
                                                                'New description (optional)',
                                                                card.description ?? ''
                                                            ) ?? '';
                                                        await updateCard(card.id, {
                                                            title: nextTitle,
                                                            description: nextDescription,
                                                        });
                                                        setStatus('Card updated.');
                                                    }}
                                                >
                                                    Edit
                                                </button>
                                                {prevColumn ? (
                                                    <button
                                                        type="button"
                                                        className="text-xs text-indigo-600"
                                                        onClick={async () => {
                                                            await moveCard(card.id, prevColumn.id, index);
                                                            setStatus('Card moved.');
                                                        }}
                                                    >
                                                        Move left
                                                    </button>
                                                ) : null}
                                                {nextColumn ? (
                                                    <button
                                                        type="button"
                                                        className="text-xs text-indigo-600"
                                                        onClick={async () => {
                                                            await moveCard(card.id, nextColumn.id, index);
                                                            setStatus('Card moved.');
                                                        }}
                                                    >
                                                        Move right
                                                    </button>
                                                ) : null}
                                            </div>
                                        </div>
                                    );
                                })}
                            {column.cards.length === 0 ? (
                                <p className="text-sm text-gray-400">No cards yet</p>
                            ) : null}
                        </div>
                    </section>
                ))}
            </div>
        </div>
    );
}
